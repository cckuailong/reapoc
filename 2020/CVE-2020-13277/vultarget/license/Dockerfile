# -----------------------------------------------------------------------------
# How to Crack Gitlab :
#   https://blog.starudream.cn/2020/01/19/6-crack-gitlab/
# -----------------------------------------------------------------------------

FROM ruby

WORKDIR /opt

RUN gem install gitlab-license
ADD ./license.rb /opt/license.rb
RUN ruby license.rb

CMD [ "bash" ]