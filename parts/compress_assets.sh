#!/bin/bash

# required env:
#   ASSETDIR


for asset in $(ls ${ASSETDIR}*.json)
do
	echo compressing ${asset}
#	gzip -fkn9 ${asset}
	zopfli -i50 ${asset}
	xz -fke9 ${asset}
done

exit 0
