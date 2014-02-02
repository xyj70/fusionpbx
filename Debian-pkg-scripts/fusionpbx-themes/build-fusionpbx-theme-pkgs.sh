#!/bin/bash
for i in accessable classic default ehanced nature
do rm -rf fusionpnx-themes-"${i}"/"${i}"
done        

svn export http://fusionpbx.googlecode.com/svn/branches/dev/fusionpbx/theme/accessable /usr/src/fusionpbx-themes/fusionpbx-themes-accessable/accessable
svn export http://fusionpbx.googlecode.com/svn/branches/dev/fusionpbx/theme/classic /usr/src/fusionpbx-themes/fusionpbx-themes-clasic/clasic
svn export http://fusionpbx.googlecode.com/svn/branches/dev/fusionpbx/theme/default /usr/src/fusionpbx-themes/fusionpbx-themes-default/default
svn export http://fusionpbx.googlecode.com/svn/branches/dev/fusionpbx/theme/ehanced /usr/src/fusionpbx-themes/fusionpbx-themes-ehanced/enhanced
svn export http://fusionpbx.googlecode.com/svn/branches/dev/fusionpbx/theme/nature /usr/src/fusionpbx-themes/fusionpbx-themes-nature/nature


for i in accessable classic default ehanced nature
do cd fusionpbx-themes-"${i}";
dpkg-buildpackage -r fakeroot -i
done        
      
cd ../
mkdir -p debs-fusionpbx-themes

mv *.changes debs-fusionpbx-themes
mv *.deb debs-fusionpbx-themes
mv *.dsc debs-fusionpbx-themes
mv *.gz debs-fusionpbx-themes

