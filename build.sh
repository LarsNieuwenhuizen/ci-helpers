#!/usr/bin/env sh

set -eux

tag=${1:-'no-tag'}

platforms="linux/amd64,linux/arm64"


if [[ $tag = 'no-tag' ]]; then
  docker buildx build --no-cache --platform=$platforms --target=install -t larsnieuwenhuizen/ci-helpers:latest --push .
else
  docker buildx build --no-cache --platform=$platforms --target=install -t larsnieuwenhuizen/ci-helpers:${tag} -t larsnieuwenhuizen/ci-helpers:latest --push .
fi
