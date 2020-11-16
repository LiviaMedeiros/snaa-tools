#!/bin/bash

# must be set!
#ASSETDIR="/tmp/snaa-tools/asset/"


for asset in $(ls ${ASSETDIR}*.json)
do
	echo gzip ${asset}
	gzip -fk9 ${asset}
done

exit 0
