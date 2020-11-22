#!/bin/bash
. ./SETME/env.sh


echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> deleting everything!" $(tput sgr0)
if [ -z ${SNAAROOT} ]; then
	echo 'you just snaapped away your files'
else
	echo 'deleting' ${SNAAROOT}
	rm -rf ${SNAAROOT}
fi

echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>> all done!" $(tput sgr0)

exit 0
