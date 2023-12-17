FROM debian:stable-slim


RUN apt-get update -qq && apt-get install -y proftpd-basic proftpd-mod-mysql proftpd-mod-crypto \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

#RUN sed -i "s/# DefaultRoot/DefaultRoot /" /etc/proftpd/proftpd.conf && sed -i "s/^\(# \)\?PassivePorts.*$/PassivePorts 60000 60100/" /etc/proftpd/proftpd.conf

COPY ./sql.conf /etc/proftpd/sql.conf
COPY ./modules.conf /etc/proftpd/modules.conf


EXPOSE 20 21

CMD ["proftpd", "--nodaemon"]
