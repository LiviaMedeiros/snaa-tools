#!/bin/bash

# SET ME
export  ROOTDIR="/SNAA/magica/"
export SNAAROOT="/tmp/snaa-tools/"
export  BASEDIR="${ROOTDIR}resource/download/asset/master/resource/"
export  ORIGDIR="${ROOTDIR}resource/download/asset/vanilla/"
export  IMWBDIR="${ROOTDIR}resource/image_web/"
export  SNAALOG="${SNAAROOT}logs/"
export  LISTDIR="${SNAAROOT}filelist/"
export ASSETDIR="${SNAAROOT}asset/"
export CHARLIST="image_native/scene/download/char_list.json"
export  MADODIR="${SNAAROOT}madomagi/"
export  MADOTAR="${HOME}/madomagi.tar.lzo"
export IROEPOCH="2017-03-17T10:39:13Z"
export DOFILES="1"

# default
export SNAASIZE="4194304"
export SNAAMULT="2"

# snaa
# unset!

# slice_1MB
#export SNAASIZE="1048576"
#export SNAAMULT="1"

# slice_64KB
#export SNAASIZE="65536"
#export SNAAMULT="1"


[ -f lib/libsnaa.sh ] || { read -p "Type password to delete all your files: " -n 4 -r; echo; [[ ! ${REPLY} =~ ^[sS]NaA$ ]] && echo 'Wrong password.' && exit 1; }
. lib/libsnaa.sh

mkdir -p ${LISTDIR}
mkdir -p ${ASSETDIR}
mkdir -p ${SNAALOG}
mkdir -p ${MADODIR}
