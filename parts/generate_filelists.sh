#!/bin/bash

# required env:
#   BASEDIR
#   LISTDIR
#   IMWBDIR


find ${BASEDIR}sound_native/fullvoice/ -type f -name "*.hca" \
     | sed "s#^${BASEDIR}##" > ${LISTDIR}fullvoice.snaa
wc -l ${LISTDIR}fullvoice.snaa

#fullvoice/section_000001
#fullvoice/section_000002
#fullvoice/section_000003
#fullvoice/section_101101
#fullvoice/section_101102
#fullvoice/section_101103
find ${BASEDIR}sound_native/voice/ -type f -name "*.hca" \
     | sed "s#^${BASEDIR}##" > ${LISTDIR}voice.snaa
wc -l ${LISTDIR}voice.snaa

find ${BASEDIR}movie/char/high/ -type f -name "*.usm" \
     | sed "s#^${BASEDIR}##" > ${LISTDIR}movie_high.snaa
wc -l ${LISTDIR}movie_high.snaa

find ${BASEDIR}movie/char/low/ -type f -name "*.usm" \
     | sed "s#^${BASEDIR}##" > ${LISTDIR}movie_low.snaa
wc -l ${LISTDIR}movie_low.snaa

find ${BASEDIR} \( -path "${BASEDIR}sound_native/fullvoice" \
                -o -path "${BASEDIR}sound_native/voice" \
                -o -path "${BASEDIR}movie/char/high" \
                -o -path "${BASEDIR}movie/char/low" \
                \) -prune -false -o -type f \
     | sed "s#^${BASEDIR}##" | grep -v '\.a[a-z][a-z]$' > ${LISTDIR}main.snaa
wc -l ${LISTDIR}main.snaa

find ${BASEDIR}scenario/json/ -type f -name "*.json" \
     | sed "s#^${BASEDIR}##" > ${LISTDIR}scenario-json.snaa
wc -l ${LISTDIR}scenario-json.snaa

find ${BASEDIR}sound_native -type f -name "*.hca" \
     | sed "s#^${BASEDIR}##" > ${LISTDIR}sound_native-hca.snaa
wc -l ${LISTDIR}sound_native-hca.snaa

find ${BASEDIR}movie -type f -name "*.usm" \
     | sed "s#^${BASEDIR}##" > ${LISTDIR}movie-usm.snaa
wc -l ${LISTDIR}movie-usm.snaa

find ${BASEDIR}image_native -type f -name "*.png" \
     | sed "s#^${BASEDIR}##" > ${LISTDIR}image_native-png.snaa
wc -l ${LISTDIR}image_native-png.snaa

find ${IMWBDIR} -type f -name "*.png" \
     | sed "s#^${IMWBDIR}##" > ${LISTDIR}image_web-png.snaa
wc -l ${LISTDIR}image_web-png.snaa

find ${BASEDIR} -type f -name "*.plist" \
     | sed "s#^${BASEDIR}##" | grep -vF 'image_native/scene/event/evt_raid_anime_map_00_ef03.plist' > ${LISTDIR}all-plist.snaa
wc -l ${LISTDIR}all-plist.snaa

exit 0
