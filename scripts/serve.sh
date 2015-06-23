#!/bin/sh

if [ "${APP_ROOT}" ]; then
  cd "${APP_ROOT}"
fi

gulp serve

