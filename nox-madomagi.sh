# no shebang cuz android is weird
mv /data/data/com.aniplex.magireco.en/files/madomagi/C5XyOsaM.json /sdcard/Download/madomagi/C5XyOsaM.json
rm -rf /data/data/com.aniplex.magireco.en/files/madomagi
ln -s /sdcard/Download/madomagi /data/data/com.aniplex.magireco.en/files/madomagi
exit 0