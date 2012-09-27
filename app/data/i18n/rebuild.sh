#!/bin/bash 
cp fa_IR.po messages.po 
cp fa_IR.po old.po 
for i in `find ../../.. | grep php$`
do 
 xgettext  --force-po -j -D ./ -L php "$i";
done;

msgmerge old.po messages.po > fa_IR.po
rm old.po
rm messages.po
