# -----------------------------------------------------------------------------
# CVE Ranger :
#   https://gitlab.com/gitlab-org/cves/-/blob/master/2021/CVE-2021-22192.json
# Gitlab Docker :
#   https://hub.docker.com/r/gitlab/gitlab-ee/tags
# -----------------------------------------------------------------------------

FROM gitlab/gitlab-ee:13.2.0-ee.0
# FROM gitlab/gitlab-ee:13.8.0-ee.0
# FROM gitlab/gitlab-ee:13.9.0-ee.0

ADD ./keys/license_key.pub /opt/gitlab/embedded/service/gitlab-rails/.license_encryption_key.pub
# RUN sed -i "s@|| STARTER_PLAN@|| ULTIMATE_PLAN@g" /opt/gitlab/embedded/service/gitlab-rails/ee/app/models/license.rb
# RUN gitlab-ctl reconfigure

EXPOSE 443
EXPOSE 80
EXPOSE 22
