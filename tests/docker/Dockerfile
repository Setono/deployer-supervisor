FROM alpine:latest

RUN apk --update add --no-cache openssh bash git rsync supervisor \
    && rm -rf /var/cache/apk/*

RUN ssh-keygen -A

COPY ssh/id_rsa.pub /root/.ssh/authorized_keys

EXPOSE 22
CMD ["/usr/sbin/sshd", "-D"]
