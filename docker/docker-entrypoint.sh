#!/usr/bin/env bash

init_app() {
  if [[ -d ${APP_ROOT}/vendor ]]; then
    return
  fi

  composer setup
}

wait_for_rabbit_mq() {
  wait-for-it.sh rabbitmq:5672 || { echo "rabbitmq did not start" && exit 1; }
}

init_app
wait_for_rabbit_mq
exec supervisord
