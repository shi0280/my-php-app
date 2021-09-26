FROM mysql:5.7

# 追加ここから
RUN apt-get update \
    && apt-get install -y \
    vim \
    locales \
    locales-all

ADD ./etc/locale.gen /etc/locale.gen

RUN locale-gen \
    update-locale
# 追加ここまで



