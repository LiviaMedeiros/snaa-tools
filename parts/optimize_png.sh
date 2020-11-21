#!/bin/bash

# required env:
#   LISTDIR
#   SNAALOG


PNGLIST=${1}
PNGDIR=${2}
ZOPJOBS=${3}

LOGFILE="${SNAALOG}${PNGLIST}.log"
PNGFILES=$(cat ${LISTDIR}${PNGLIST}.snaa)
ZOPZOP="zopflipng --iterations=500 --filters=01234mepb -y"

echo 'optimize png START:' ${PNGLIST}
: > ${LOGFILE}
if [ -z ${ZOPJOBS} ]; then
	for PNGFILE in ${PNGFILES}
	do
		echo -ne "\033[K${PNGFILE}\r"
		${ZOPZOP} ${PNGDIR}${PNGFILE} ${PNGDIR}${PNGFILE} >> ${LOGFILE}
	done
else
	parallel -j${ZOPJOBS} ${ZOPZOP} ${PNGDIR}{} ${PNGDIR}{} ::: ${PNGFILES} >> ${LOGFILE}
fi
echo -e '\033[Koptimize png DONE:' ${PNGLIST}


exit 0