# ci-helpers
Library with scripts to help in CI tasks like defining next versions, releasing versions, updating projects etc..

## Console commands
There are currently two scripts.

1. `bin/console version:define`

    This takes the last created git tag, lists the commits created after the commit.
    And defines the next SemVer tag based on fixes, features or breaking changes.
2. `bin/console version:release`

    This actually creates the release commit and tags it with the next SemVer version and pushes it to master

## Docker image
The image created is `larsnieuwenhuizen/ci-helpers`

## Pipeline usage

To use it simply run a pipeline step with the image.
Copy your code *with* git history into /app/code in the container.
And run the console command you want.

Simple example:
```yaml
- mv ./* /app/code/
- cp -r .git /app/code/
- cd /app
- bin/console version:define -vvv
```

## Git hooks
The SemVer check which version is next is done based on git log.
You need to create your git commit messages in a correct manner.

To check your commit messages on git commit you can install the following hook and configuration file.

In your terminal go to your git project root and run:

```shell
curl https://raw.githubusercontent.com/LarsNieuwenhuizen/ci-helpers/main/bin/install-commit-msg-hook.sh | bash
```

What you'll get is the `.git/hooks/commit-msg` in your poject.
The json configuration file setting which commit message prefixes you'll allow.

Note: Credits for this hook go to: https://github.com/craicoverflow

Blog with the script:
https://dev.to/craicoverflow/enforcing-conventional-commits-using-git-hooks-1o5p
