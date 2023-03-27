#!/usr/bin/env sh

set -eux pipefail

$platforms="linux/amd64,linux/arm64"

docker buildx build --no-cache --platform $platforms --target=install -t larsnieuwenhuizen/ci-helpers:latest --push .
