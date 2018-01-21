SHELL=/bin/bash

all: init

init:
	../vendor/bin/propel sql:build --overwrite
	../vendor/bin/propel model:build
	../vendor/bin/propel config:convert
	rm -rf ../src/Model/Base/
	rm -rf ../src/Model/Map/
	mv generated-classes/Qrawler/Model/Base ../src/Model/Base/
	mv generated-classes/Qrawler/Model/Map ../src/Model/Map/
	rm -rf generated-classes/
