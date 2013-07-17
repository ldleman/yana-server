#!/bin/bash

base="/var/www/yana-server/plugins/camera"

cd $base

raspivid -n -w 720 -h 405 -fps 25 -vf -t 86400000 -b 1800000 -o - \
| ffmpeg -y \
    -i - \
    -c:v copy \
    -map 0:0 \
    -f ssegment \
    -segment_time 4 \
    -segment_format mpegts \
    -segment_list "$base/stream.m3u8" \
    -segment_list_size 720 \
    -segment_list_flags live \
    -segment_list_type m3u8 \
    "segments/%08d.ts" 


trap "rm stream.m3u8 segments/*.ts" EXIT

# vim:ts=2:sw=2:sts=2:et:ft=sh