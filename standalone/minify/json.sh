#!/bin/bash

command -v jq &>/dev/null || { echo "Error: jq is not installed."; exit 1; }
command -v parallel &>/dev/null || { echo "Error: parallel is not installed."; exit 1; }
command -v pv &>/dev/null || { echo "Error: pv is not installed."; exit 1; }

compact_json() {
    jq -c . "$1" > "$1.tmp" && mv "$1.tmp" "$1"
}
export -f compact_json

[ $# -ne 1 ] && { echo "Usage: $0 <directory>"; exit 1; }
TARGET_DIR="$1"
[ ! -d "$TARGET_DIR" ] && { echo "Error: Directory '$TARGET_DIR' does not exist."; exit 1; }

json_files=($(find "$TARGET_DIR" -type f -name "*.json"))
[ ${#json_files[@]} -eq 0 ] && { echo "No JSON files found in the specified directory."; exit 1; }

total_files=${#json_files[@]}
echo "Compacting ${total_files} JSON files..."
printf "%s\n" "${json_files[@]}" | pv -l -s "$total_files" | parallel -j "$(nproc)" compact_json
echo "Done!"
exit 0
