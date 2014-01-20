#!/bin/sh
exec /usr/bin/ssh -o StrictHostKeyChecking=no -i $PATH_TO_PRIVATE_KEY "$@"