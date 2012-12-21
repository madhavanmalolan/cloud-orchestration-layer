#!/bin/bash
grep processor /proc/cpuinfo | wc -l > info.data
grep MemFree /proc/meminfo >> info.data
df /tmp --total -h| grep total >> info.data
dmesg | grep -i virtual > virtual.data
