#!/bin/bash

# required env:
#   BASEDIR
#   LISTDIR
#   MADODIR
#   MADOTAR


MADOFILES=$(cat ${LISTDIR}main.txt ${LISTDIR}fullvoice.txt ${LISTDIR}voice.txt ${LISTDIR}movie_high.txt)
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