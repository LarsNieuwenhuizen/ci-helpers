#!/bin/bash

## note: Download and place the commit message hook
curl https://raw.githubusercontent.com/LarsNieuwenhuizen/ci-helpers/main/git-hooks/commit-msg;
mv commit-msg .git/hooks/

# note: Download the json config file the hook uses
curl https://raw.githubusercontent.com/LarsNieuwenhuizen/ci-helpers/main/git-hooks/commit-msg.config.json
