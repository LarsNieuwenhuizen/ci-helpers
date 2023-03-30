#!/usr/bin/env bash

set -eux

## note: Download and place the commit message hook
wget https://raw.githubusercontent.com/LarsNieuwenhuizen/ci-helpers/main/git-hooks/commit-msg;
mv commit-msg .git/hooks/commit-msg;
chmod +x .git/hooks/commit-msg;

# note: Download the json config file the hook uses
wget https://raw.githubusercontent.com/LarsNieuwenhuizen/ci-helpers/main/git-hooks/commit-msg.config.json
