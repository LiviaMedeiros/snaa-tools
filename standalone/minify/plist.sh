#!/bin/bash

command -v xmllint &>/dev/null || { echo "Error: xmllint is not installed."; exit 1; }
command -v parallel &>/dev/null || { echo "Error: parallel is not installed."; exit 1; }
command -v pv &>/dev/null || { echo "Error: pv is not installed."; exit 1; }

compact_xml() {
    xmllint --noblanks "$1" > "$1.tmp" && mv "$1.tmp" "$1"
}
export -f compact_xml

[ $# -ne 1 ] && { echo "Usage: $0 <directory>"; exit 1; }
TARGET_DIR="$1"
[ ! -d "$TARGET_DIR" ] && { echo "Error: Directory '$TARGET_DIR' does not exist."; exit 1; }

xml_files=($(find "$TARGET_DIR" -type f -name "*.plist"))
[ ${#xml_files[@]} -eq 0 ] && { echo "No plist files found in the specified directory."; exit 1; }

total_files=${#xml_files[@]}
echo "Compacting ${total_files} plist files..."
printf "%s\n" "${xml_files[@]}" | pv -l -s "$total_files" | parallel -j "$(nproc)" compact_xml
echo "Done!"
exit 0
