#!/usr/bin/env bash

## note: Download and place the commit message hook
COMMIT_MSG_HOOK_FILE=.git/hooks/commit-msg
if [[ ! "$COMMIT_MSG_HOOK_FILE" ]]; then
  wget https://raw.githubusercontent.com/LarsNieuwenhuizen/ci-helpers/main/git-hooks/commit-msg;
  mv commit-msg .git/hooks/commit-msg;
  chmod +x .git/hooks/commit-msg;
else
  echo "Commit message hook already exists!"
fi

# note: Download the json config file the hook uses
COMMIT_MSG_CONFIG_FILE=commit-msg.config.json
if [[ ! "$COMMIT_MSG_CONFIG_FILE" ]]; then
  wget https://raw.githubusercontent.com/LarsNieuwenhuizen/ci-helpers/main/git-hooks/commit-msg.config.json
else
  echo "Config file already exists!"
fi
