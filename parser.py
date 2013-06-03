from bs4 import BeautifulSoup
import MySQLdb as mdb
import urllib2 as ulib
import urllib
import json
from pprint import pprint

# Open database connection
db = mdb.connect("127.0.0.1","root","","news2map",3306)
# prepare a cursor object using cursor() method
cursor = db.cursor()

opener = ulib.build_opener()
ulib.install_opener(opener)


def parse(page_url,page_num,divstr,str1,pstr,strlin,strdiv,ttle,db1,max):
	try:
		data= ulib.urlopen(page_url).read()
		print "helo"
	except:
		print "Error in opening the url:",page_url
		return
	soup = BeautifulSoup(data)
	try:
		first= soup.find(strdiv,divstr)
	except:
		print "Error in finding the"+divstr+" in url:",page_url
	order=max*(page_num-1)
	inpage=0
	for link in first.find_all(strlin):
		inpage+=1
		order1=order+inpage
		#print link
		try:
			if ttle!="":
				title=link.find('p','title')
			else:
				title=link.find('p')
			for url in title.find_all('a'):
				url=url['href']
				url=str1+url
		except:
			print "Error in finding the title in url:",page_url
			continue
		try:
			snippet=link.find('p',pstr).get_text()
		except:
			print "Error in finding the snippet_class in url:",page_url
			continue
		title=title.get_text()
		title=title.replace("'","\\'")
		url=url.replace("'","\\'")
		title=title.replace("\"","\\\"")
		snippet=snippet.replace("'","\\'")
		snippet=snippet.replace("\"","\\\"")
		print title + " " + url + " " + snippet + "\n"
		if db1=="moneycontrol":
			query="INSERT into `moneycontrol` (`url`, `title`,`snippet`, `order`) VALUES('"+url+"','"+title+"','"+snippet+"',"+(str)(order1)+")"
			#print query
			search_query="select * from `moneycontrol` where `url`='"+url+"'"
		elif db1== "economictimes": 
			query="INSERT into `economictimes` (`url`, `title`,`snippet`, `order`) VALUES('"+url+"','"+title+"','"+snippet+"',"+(str)(order1)+")"
			#print query
			search_query="select * from `economictimes` where `url`='"+url+"'"
		try:
			cursor.execute(search_query)
			pass
		except Exception, e:
			print repr(e)
			print "Error in search_query:",search_query
			continue
		row=cursor.fetchone()
		#row=0
		if not row:
			try:
				cursor.execute(query)
				print "Successful"
			except Exception, e:
				print repr(e)
				try:
					db.rollback()
				except:
					print "rollback error"
				continue
			try:
				db.commit()
			except:
				print "Error in committ"
				continue
		else:
			if db1=="moneycontrol":
				update_query="UPDATE `moneycontrol` SET `order`="+(str)(order)+" where `url`='"+url+"'"
			elif db1== "economictimes": 
				update_query="UPDATE `economictimes` SET `order`="+(str)(order)+" where `url`='"+url+"'"
			cursor.execute(update_query)
			print "Already exists"
		print "\n"



#comment starts

page_url1='http://www.moneycontrol.com/news/news-All.html'
divstr1='PL15 MT15 PR20 PB20'
str1='www.moneycontrol.com'
pstr1='MT7'
strlin1='li'
strdiv1='div'
print page_url1
title1='title'
db1='moneycontrol'
max=31
parse(page_url1,1,divstr1,str1,pstr1,strlin1,strdiv1,title1,db1,max)

for num in range(2,10):
	print num
	page_url1='http://www.moneycontrol.com/news/all-news-All-'+(str)(num)+'-next-0.html'
	parse(page_url1,num,divstr1,str1,pstr1,strlin1,strdiv1,title1,db1,max)


page_url2 = "http://economictimes.indiatimes.com/news/news-by-industry/banking/finance/finance/articlelist/msid-13358311,page-1.cms"
divstr2="inner1"
str2="www.economictimes.indiatimes.com"
pstr2="normtxt"
strlin2='div'
strdiv2='td'
title2=""
max=10
db1='economictimes'
parse(page_url2,1,divstr2,str2,pstr2,strlin2,strdiv2,title2,db1,max)

for num in range(2,10):
    print num
    page_url2='http://economictimes.indiatimes.com/news/news-by-industry/banking/finance/finance/articlelist/msid-13358311,page-'+(str)(num)+'.cms'
    parse(page_url2,num,divstr2,str2,pstr2,strlin2,strdiv2,title2,db1,max)


db.close()

