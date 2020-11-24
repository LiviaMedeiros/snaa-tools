#!/bin/bash

function snaa_print {
	echo $(tput bold) ">>>" $(date +%H:%M:%S.%3N) ">>>" ${1} $(tput sgr0)
}