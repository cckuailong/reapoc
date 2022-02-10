#!/bin/sh

# $Id: gettextize.sh 1068 2008-04-06 17:26:12Z liedekef $

name=`basename "$0"`
ldir="locale"

while [ ! -z "$1" ]; do
  case "$1" in
  -d)
    shift
    ldir="$1"
    ;;
  *)
    echo "usage: $name [-d directory]"
    exit 1
	;;
  esac
  shift
done

langs=`echo "$ldir"/[a-z][a-z]_[A-Z][A-Z] | sed "s,$ldir/,,g"`
cwd=`pwd`
dir=`basename "$cwd"`

script_dir=`dirname "$0" | sed "s:^\\.:$cwd:"`

echo "==> Extracting language strings [$langs]"
(cd ..; \
  $script_dir/pgettext.pl -o - -s \
    `find "$dir" -name \*.php -o -name \*.inc` | \
	sed s/charset=CHARSET/charset=UTF-8/ > "$dir/locale/new.po";)

date=`date "+%Y-%m-%d %H:%M%z" | sed s/\\n//`
cd "$ldir"
for i in $langs; do
  echo ''
  /bin/echo -n "==> Merging strings for $i "
  msgmerge --strict -o "$i.po" "$i/LC_MESSAGES/messages.po" new.po
#   sed -E "s/Project-Id-Version: $package-.*\\\\n/Project-Id-Version: $package-$version\\\\n/;
#	s/PO(-Revision|T-Creation)-Date: .*\\\\n/PO\\1-Date: $date\\\\n/" > "$i.po"
  echo "==> Compiling strings for $i"
# We don't want the fuzzy translations compiled, people should update the language files
#  msgfmt --strict -f -c -v -o "$i.mo" "$i.po"
  msgfmt --strict -c -v -o "$i.mo" "$i.po"
  mv "$i.po" "$i/LC_MESSAGES/messages.po"
  mv "$i.mo" "$i/LC_MESSAGES/messages.mo"
done
