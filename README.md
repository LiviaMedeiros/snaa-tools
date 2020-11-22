# snaa-tools

some trash scripts.

don't forget to set environment properly. in both files.
snaa-tools are non-interactive and they will snaap your filesystem from any mistake.

### requirements
php, bash, coreutils, findutils, tar, grep, sed, parallel, zopfli, etc.

### examples
example usage for brave people:
```sh
$ cd snaa-tools && ./make_assets.sh && ./make_madomagi.sh
```

example usage for cautious people:
```sh
$ vim snaa-tools/make_assets.sh
```

example usage if you don't know what it is:
```sh
$ exit
```

### default configuration
`/tmp/snaa-tools/` - output/temp files

`/SNAA/` - your files (not included)

`${HOME}/` - resulting tarball

voices: enabled

video quality: high

chunking: default (4194304x2)

etag: "MD5SUM" (aws)

<p align="right"><img src="https://xn--80aalyho.xn--p1ai/magireco/NAgitan/img/mumiwhy.png" /></p>
