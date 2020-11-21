#!/bin/bash

# required env:
#   LISTDIR
#   SNAALOG


PNGLIST=${1}
PNGDIR=${2}

LOGFILE="${SNAALOG}${PNGLIST}.log"
PNGFILES=$(cat ${LISTDIR}${PNGLIST}.snaa)

echo 'optimize png START:' ${PNGLIST}
: > ${LOGFILE}
for PNGFILE in ${PNGFILES}
do
	echo -ne "\033[K${PNGFILE}\r"
	#zopflipng -y ${PNGDIR}${PNGFILE} ${PNGDIR}${PNGFILE} >> ${LOGFILE}
	zopflipng --iterations=500 --filters=01234mepb -y ${PNGDIR}${PNGFILE} ${PNGDIR}${PNGFILE} >> ${LOGFILE}
done
echo -e '\033[Koptimize png DONE:' ${PNGLIST}


exit 0
