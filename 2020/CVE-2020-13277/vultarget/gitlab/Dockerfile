# -----------------------------------------------------------------------------
# CVE Ranger :
#   https://gitlab.com/gitlab-org/cves/-/blob/master/2020/CVE-2020-13277.json
# Gitlab Docker :
#   https://hub.docker.com/r/gitlab/gitlab-ee/tags
# -----------------------------------------------------------------------------

FROM gitlab/gitlab-ee:10.6.0-ee.0
# FROM gitlab/gitlab-ee:12.10.0-ee.0
# FROM gitlab/gitlab-ee:13.0.0-ee.0

ADD ./keys/license_key.pub /opt/gitlab/embedded/service/gitlab-rails/.license_encryption_key.pub
# RUN sed -i "s@|| STARTER_PLAN@|| ULTIMATE_PLAN@g" /opt/gitlab/embedded/service/gitlab-rails/ee/app/models/license.rb
# RUN gitlab-ctl reconfigure

EXPOSE 443
EXPOSE 80
EXPOSE 22
