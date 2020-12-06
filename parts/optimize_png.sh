#!/bin/bash

# required env:
#   LISTDIR
#   SNAALOG


PNGLIST=${1}
export PNGDIR=${2}
ZOPJOBS=${3}

export ZOPZOP="zopflipng --iterations=500 --filters=01234mepb -y"
export LOGFILE="${SNAALOG}${PNGLIST}.log"
export DONELIST="${SNAALOG}${PNGLIST}-done.snaa"
PNGFILES=$(cat ${LISTDIR}${PNGLIST}.snaa)

echo 'optimize png START:' ${PNGLIST}
: > ${LOGFILE}
touch ${DONELIST}
if [ -z ${ZOPJOBS} ]; then
	for PNGFILE in ${PNGFILES}
	do
		echo -ne "\033[K${PNGFILE}\r"
		optimize_png ${PNGFILE}
	done
else
	parallel -j${ZOPJOBS} optimize_png {} ::: ${PNGFILES}
fi
echo -e '\033[Koptimize png DONE:' ${PNGLIST}


exit 0
