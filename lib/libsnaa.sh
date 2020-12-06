#!/bin/bash

function snaa_print {
	echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>>" ${1} $(tput sgr0)
}

optimize_png() {
	grep -Fq ${1} ${DONELIST} || ${ZOPZOP} ${PNGDIR}${1} ${PNGDIR}${1} >> ${LOGFILE}
	echo ${1} >> ${DONELIST}
}
export -f optimize_png
