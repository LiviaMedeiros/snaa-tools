#!/bin/bash

command -v zopflipng &>/dev/null || { echo "Error: zopflipng is not installed."; exit 1; }
command -v parallel &>/dev/null || { echo "Error: parallel is not installed."; exit 1; }
command -v pv &>/dev/null || { echo "Error: pv is not installed."; exit 1; }

optimize_png() {
    zopflipng --iterations=500 --filters=01234mepb --lossy_8bit --lossy_transparent "$1" "$1.tmp" 2>/dev/null && mv "$1.tmp" "$1"
}
export -f optimize_png

[ $# -ne 1 ] && { echo "Usage: $0 <directory>"; exit 1; }
TARGET_DIR="$1"
[ ! -d "$TARGET_DIR" ] && { echo "Error: Directory '$TARGET_DIR' does not exist."; exit 1; }

png_files=($(find "$TARGET_DIR" -type f -name "*.png"))
[ ${#png_files[@]} -eq 0 ] && { echo "No png files found in the specified directory."; exit 1; }

total_files=${#png_files[@]}
echo "Optimizing ${total_files} png files..."
printf "%s\n" "${png_files[@]}" | pv -l -s "$total_files" | parallel -j "$(nproc)" optimize_png
echo "Done!"
exit 0
