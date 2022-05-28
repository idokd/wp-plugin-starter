#!/bin/bash

# TODO: make sure it works, and change to work on main folder name

PLUGIN_FILENAME="plugin-name.php"
PLUGIN_I10N_DOMAIN="plugin-name"

VERSIONS=`git rev-list --tags --max-count=1`
LAST_VERSION=`git describe --tags $VERSIONS`

increment_version ()
{
  declare -a part=( ${1//\./ } )
  declare new
  declare -i carry=1
  for (( CNTR=${#part[@]}-1; CNTR>=0; CNTR-=1 )); do
    len=${#part[CNTR]}
    new=$((part[CNTR]+carry))
    [ ${#new} -gt $len ] && carry=1 || carry=0
    [ $CNTR -gt 0 ] && part[CNTR]=${new: -len} || part[CNTR]=${new}
  done
  new="${part[*]}"
  echo -e "${new// /.}"
}

echo "Updating language file..."
GIT_ROOT=`git rev-parse --show-toplevel`
wp i18n make-pot $GIT_ROOT "$GIT_ROOT/languages/$PLUGIN_I10N_DOMAIN.pot"
VERSION=`increment_version $LAST_VERSION`

echo "Poping version to: $VERSION"
sed -i "" "s/Version:[[:blank:]]*\([[:digit:]]*\.*\)\{1,4\}/Version: ${VERSION}/" $PLUGIN_FILENAME
sed -i "" "s/Stable tag:[[:blank:]]*\([[:digit:]]*\.*\)\{1,4\}/Stable tag: $VERSION/" ./readme.txt
git add .
git commit -m "$VERSION"
git push
git tag -a $VERSION -m "$LAST_VERSION -> $VERSION"
git push origin $VERSION
