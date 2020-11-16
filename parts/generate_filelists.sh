#!/bin/bash

# required env:
#   BASEDIR
#   LISTDIR


find ${BASEDIR}sound_native/fullvoice/ -type f | sed "s#^${BASEDIR}##" | grep -v '\.a[a-z][a-z]$' > ${LISTDIR}fullvoice.txt
wc -l ${LISTDIR}fullvoice.txt

#fullvoice/section_000001
#fullvoice/section_000002
#fullvoice/section_000003
#fullvoice/section_101101
#fullvoice/section_101102
#fullvoice/section_101103
find ${BASEDIR}sound_native/voice/ -type f | sed "s#^${BASEDIR}##" | grep -v '\.a[a-z][a-z]$' > ${LISTDIR}voice.txt
wc -l ${LISTDIR}voice.txt

find ${BASEDIR}movie/char/high/ -type f | sed "s#^${BASEDIR}##" | grep -v '\.a[a-z][a-z]$' > ${LISTDIR}movie_high.txt
wc -l ${LISTDIR}movie_high.txt

find ${BASEDIR}movie/char/low/ -type f | sed "s#^${BASEDIR}##" | grep -v '\.a[a-z][a-z]$' > ${LISTDIR}movie_low.txt
wc -l ${LISTDIR}movie_low.txt

find ${BASEDIR} \( -path "${BASEDIR}sound_native/fullvoice" \
                -o -path "${BASEDIR}sound_native/voice" \
                -o -path "${BASEDIR}movie/char/high" \
                -o -path "${BASEDIR}movie/char/low" \
                \) -prune -false -o -type f | sed "s#^${BASEDIR}##" | grep -v '\.a[a-z][a-z]$' > ${LISTDIR}main.txt
wc -l ${LISTDIR}main.txt


exit 0
