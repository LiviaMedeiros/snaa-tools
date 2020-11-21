#!/bin/bash

# required env:
#   ASSETDIR


for asset in $(ls ${ASSETDIR}*.json)
do
	echo gzip ${asset}
#	gzip -fkn9 ${asset}
	zopfli -i50 ${asset}
done

exit 0
