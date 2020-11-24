#!/bin/bash
[ -f ./SETME/env.sh ] && . ./SETME/env.sh || { echo 'snaa'; exit 1; }


snaa_print "deleting everything!"
if [ -z ${SNAAROOT} ]; then
	echo 'you just snaapped away your files'
else
	echo 'deleting' ${SNAAROOT}
	rm -rf ${SNAAROOT}
fi

snaa_print "all done!"

exit 0
