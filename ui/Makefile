all: build-image build

build-image:
	docker build -t qrawler/ui-build .

build:
	docker rm -f qrawler-ui-build || true
	docker run -it --name qrawler-ui-build qrawler/ui-build
	docker cp qrawler-ui-build:/app/build $@
	mkdir -p $(TARGET)/nginx/var
	rm -rf $(TARGET)/nginx/var/www
	mv $@ $(TARGET)/nginx/var/www

clean:
	docker rm -f qrawler-ui-build || true
