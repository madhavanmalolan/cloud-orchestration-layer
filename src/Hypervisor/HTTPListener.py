import BaseHTTPServer
import urlparse
import socket

HOST_NAME = ""
PORT_NUMBER = 8081

class MyHTTPHandler(BaseHTTPServer.BaseHTTPRequestHandler):
    def do_HEAD(self):
        self.send_response(200)
	self.send_header("Content-type","text/JSON")
	self.end_headers()
    def do_GET(self):
        self.send_response(200)
	#Set HTTP Headers
	#Set Content Type(Return) as JSON
	self.send_header("Content-type","text/JSON")
	self.end_headers()

	#Process the URL to get the GET Variables
	#Dictionary consisting of {variable_name: variable_value} pairs
	get_params = self.get_URL_params(self.path)

	#Process the request
	response = self.process_request(self.path.split("?")[0], get_params)

	self.wfile.write(response)
    def process_request(self, absolute_path, get_params):
        return "Unknown request. Handler Failed"


    def get_URL_params(self, path):
        variables_section_of_url = path.split("?")[-1]
	dictionary_of_variables = urlparse.parse_qs(variables_section_of_url)
	return dictionary_of_variables






def main():
    server_class = BaseHTTPServer.HTTPServer
    global HOST_NAME, PORT_NUMBER
    try:
        httpd = server_class((HOST_NAME, PORT_NUMBER), MyHTTPHandler)
        httpd.serve_forever()
    except Exception as e:
        print e
if __name__=="__main__":
    main()
