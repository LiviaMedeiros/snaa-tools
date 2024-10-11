#!/bin/bash

command -v jpegoptim &>/dev/null || { echo "Error: jpegoptim is not installed."; exit 1; }
command -v parallel &>/dev/null || { echo "Error: parallel is not installed."; exit 1; }
command -v pv &>/dev/null || { echo "Error: pv is not installed."; exit 1; }

optimize_jpg() {
    jpegoptim --strip-all --all-progressive "$1" --quiet
}
export -f optimize_jpg

[ $# -ne 1 ] && { echo "Usage: $0 <directory>"; exit 1; }
TARGET_DIR="$1"
[ ! -d "$TARGET_DIR" ] && { echo "Error: Directory '$TARGET_DIR' does not exist."; exit 1; }

jpg_files=($(find "$TARGET_DIR" -type f -name "*.jpg"))
[ ${#jpg_files[@]} -eq 0 ] && { echo "No jpg files found in the specified directory."; exit 1; }

total_files=${#jpg_files[@]}
echo "Optimizing ${total_files} jpg files..."
printf "%s\n" "${jpg_files[@]}" | pv -l -s "$total_files" | parallel -j "$(nproc)" optimize_jpg
echo "Done!"
exit 0
