<?php

class non_priced {
	
	function showNonPricedGoods() { $db=DbSingleton::getTokoDb(); $list=""; $i=0;
		$r=$db->query("select t2p.art_id from T2_ARTICLES_PRICE_RATING t2p
		    left outer join T2_ARTICLES_STOCK t2s on t2s.ART_ID = t2p.ART_ID
		where t2s.amount>0 and t2p.price_1=0 group by t2p.art_id;"); $n=$db->num_rows($r);
   		if ($n>0) {
			for ($i=1;$i<=$n;$i++){
				$art_id=$db->result($r,$i-1,"art_id");
				list($article_nr_displ,,$brand_name,)=$this->getArticleNrDisplBrand($art_id);
				$list.="<tr>
					<td>$i</td>
					<td>$art_id</td>
					<td>$article_nr_displ</td>
					<td>$brand_name</td>
				</tr>";
			}
		}
		$maxi=$i;
		$r=$db->query("select art_id from T2_SUPPL_IMPORT where price_suppl=0 and stock_suppl>0 and art_id!=0 group by art_id;"); $n=$db->num_rows($r);
	    if ($n>0) {
			for ($i=1;$i<=$n;$i++){$maxi++;
				$art_id=$db->result($r,$i-1,"art_id");
				list($article_nr_displ,,$brand_name,)=$this->getArticleNrDisplBrand($art_id);
				$list.="<tr>
					<td>$maxi</td>
					<td>$art_id</td>
					<td>$article_nr_displ</td>
					<td>$brand_name</td>
				</tr>";
			}
		}
		return $list;
	}
	
	function getArticleNrDisplBrand($art_id) { $db=DbSingleton::getTokoDb();
		$r=$db->query("select t2a.ART_ID, t2a.BRAND_ID, t2a.ARTICLE_NR_DISPL, t2a.ARTICLE_NR_SEARCH, t2b.BRAND_NAME 
		from T2_ARTICLES t2a 
		    left outer join T2_BRANDS t2b on t2b.BRAND_ID=t2a.BRAND_ID 
		where t2a.ART_ID='$art_id' limit 1;");
        $brand_id=$db->result($r,0,"BRAND_ID");
        $article_nr_displ=$db->result($r,0,"ARTICLE_NR_DISPL");
        $brand_name=$db->result($r,0,"BRAND_NAME");
        $article_nr_search=$db->result($r,0,"ARTICLE_NR_SEARCH");
		return array($article_nr_displ,$brand_id,$brand_name,$article_nr_search);
	}
	
}