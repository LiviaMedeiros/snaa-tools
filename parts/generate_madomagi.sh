#!/bin/bash

# required env:
#   BASEDIR
#   LISTDIR
#   MADODIR
#   MADOTAR


echo -ne "loading...\r"
MADOFILES=$(cat ${LISTDIR}main.snaa ${LISTDIR}fullvoice.snaa ${LISTDIR}voice.snaa ${LISTDIR}movie_high.snaa)
MADOSUBDIRS=$(echo ${MADOFILES} | xargs -n 1 dirname | sort -u)

echo 'madomagi directory structure START'
for MADOSUBDIR in ${MADOSUBDIRS}
do
	echo -ne "\033[K${MADOSUBDIR}\r"
	mkdir -p ${MADODIR}resource/${MADOSUBDIR}
done
rmdir ${MADODIR}resource/movie/char/high
echo -e '\033[Kmadomagi directory structure DONE'

echo 'madomagi symlink START'
for MADOFILE in ${MADOFILES}
do
	echo -ne "\033[K${MADOFILE}\r"
	ln -sf ${BASEDIR}${MADOFILE} ${MADODIR}resource/$(echo ${MADOFILE} | sed 's#movie/char/high/#movie/char/#')
done
echo -e '\033[Kmadomagi symlink DONE'

echo 'madomagi.tar.lzo START:' ${MADOTAR}
tar --lzop -chf ${MADOTAR} -C $(dirname ${MADODIR}) madomagi
echo 'madomagi.tar.lzo DONE:' ${MADOTAR}

exit 0
