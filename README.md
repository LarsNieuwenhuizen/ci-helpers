# ci-helpers
Library with scripts to help in CI tasks like defining next versions, releasing versions, updating projects etc..

## Console commands
There are currently two scripts.

1. `bin/console version:define`

    This takes the last created git tag, lists the commits created after the commit.
    And defines the next SemVer tag based on fixes, features or breaking changes.
2. `bin/console version:release`

    This actually creates the release commit and tags it with the next SemVer version and pushes it to master
