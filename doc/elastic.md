TermQuery : exact value, like id, username, price, not for text

Analyser
====
http://localhost:5601/app/kibana#/dev_tools/console

GET bottin/_analyze
{
  "analyzer" : "french_heavy",
  "text" : "TAVERNE LE PALACE"
}


