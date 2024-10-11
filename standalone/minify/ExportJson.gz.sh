#!/bin/bash

command -v zopfli &>/dev/null || { echo "Error: zopfli is not installed."; exit 1; }
command -v jq &>/dev/null || { echo "Error: jq is not installed."; exit 1; }
command -v parallel &>/dev/null || { echo "Error: parallel is not installed."; exit 1; }
command -v pv &>/dev/null || { echo "Error: pv is not installed."; exit 1; }

minify_json_gz() {
    local file="$1"
    local tmp_json="${file%.gz}.json"
    gunzip -c "$file" | jq -c . > "$tmp_json" && zopfli --i64 -c "$tmp_json" > "$file" && rm "$tmp_json"
}
export -f minify_json_gz

[ $# -ne 1 ] && { echo "Usage: $0 <directory>"; exit 1; }
TARGET_DIR="$1"
[ ! -d "$TARGET_DIR" ] && { echo "Error: Directory '$TARGET_DIR' does not exist."; exit 1; }

gz_files=($(find "$TARGET_DIR" -type f -name "*.ExportJson.gz"))
[ ${#gz_files[@]} -eq 0 ] && { echo "No .ExportJson.gz files found in the specified directory."; exit 1; }

total_files=${#gz_files[@]}
echo "Minifying ${total_files} .ExportJson.gz files..."
printf "%s\n" "${gz_files[@]}" | pv -l -s "$total_files" | parallel -j "$(nproc)" minify_json_gz
echo "Done!"
exit 0
