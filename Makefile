all:
	if [[ -e begateway-1.0.zip ]]; then rm begateway-1.0.zip; fi
	zip -r begateway-1.0.zip modules -x "*/test/*" -x "*/.git/*" -x "*/examples/*"
