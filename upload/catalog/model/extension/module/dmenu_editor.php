<?php
/**
 * Model Module D.Menu Editor Class
 * 
 * @version 1.0.2
 * 
 * @author D.art <d.art.reply@gmail.com>
 */

class ModelExtensionModuleDMenuEditor extends Model {

    /**
     * Get Categories data.
     * 
     * @param int $parent_id
     * 
     * @return array $query->rows
     */
    public function getCategories($parent_id = 0) {
        $query = $this->db->query("SELECT c.category_id, cd.name, c.top, c.column FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");

        return $query->rows;
    }

    /**
     * Get All Categories data.
     * 
     * @return array $query->rows
     */
    public function getCategoriesAll() {
        $query = $this->db->query("SELECT c.category_id, c.parent_id, c.top, c.column, cd.name FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");

        return $query->rows;
    }

    /**
     * Get Category data.
     * 
     * @param int $category_id
     * 
     * @return array $query->row
     */
    public function getCategory($category_id) {
        $query = $this->db->query("SELECT TRIM('0_' FROM CONCAT(GROUP_CONCAT(c2.parent_id ORDER BY cp.level SEPARATOR '_'), '_', c1.category_id)) AS path FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) WHERE c1.category_id = '" . (int)$category_id . "'");

        return $query->row;
    }

    /**
     * Get Product data.
     * 
     * @param int $product_id
     * 
     * @return array $product_data
     */
    public function getProduct($product_id) {
        $path = '';

        // Get Product from DB.
        $query_product = $this->db->query("SELECT ptc.category_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_category ptc ON (p.product_id = ptc.product_id) WHERE p.product_id = '" . (int)$product_id . "'");

        // Get Category ID.
        if ($query_product->row['category_id']) {
            $category_id = $query_product->row['category_id'];
        } else {
            $category_id = 0;
        }

        // Get category Path.
        $query_category_path = $this->db->query("SELECT TRIM('0_' FROM CONCAT(GROUP_CONCAT(c2.parent_id ORDER BY cp.level SEPARATOR '_'), '_', c1.category_id)) AS path FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) WHERE cp.category_id = '" . (int)$category_id . "'");

        if (isset($query_category_path->row['path'])) {
            $path = $query_category_path->row['path'];
        }

        $product_data = array(
            'path' => $path
        );

        return $product_data;
    }

    /**
     * Get ocStore Blog Category data.
     * 
     * @param int $blog_category_id
     * 
     * @return array $blog_category_data
     */
    public function getBlogCategory($blog_category_id) {
        $blog_category_data = array();

        $table_name = DB_PREFIX . 'blog_category';

        if ($this->tableExists($table_name)) {
            $query = $this->db->query("SELECT TRIM('0_' FROM CONCAT(GROUP_CONCAT(bc2.parent_id ORDER BY bcp.level SEPARATOR '_'), '_', bc1.blog_category_id)) AS path FROM " . DB_PREFIX . "blog_category_path bcp LEFT JOIN " . DB_PREFIX . "blog_category bc1 ON (bcp.blog_category_id = bc1.blog_category_id) LEFT JOIN " . DB_PREFIX . "blog_category bc2 ON (bcp.path_id = bc2.blog_category_id) WHERE bc1.blog_category_id = '" . (int)$blog_category_id . "'");

            $blog_category_data = $query->row;
        }

        return $blog_category_data;
    }

    /**
     * Get ocStore Blog Article data.
     * 
     * @param int $blog_article_id
     * 
     * @return array $blog_article_data
     */
    public function getBlogArticle($blog_article_id) {
        $path = '';

        $blog_article_data = array();

        $table_name = DB_PREFIX . 'article';

        if ($this->tableExists($table_name)) {
            // Get Article from DB.
            $query_article = $this->db->query("SELECT atbc.blog_category_id FROM " . DB_PREFIX . "article a LEFT JOIN " . DB_PREFIX . "article_to_blog_category atbc ON (a.article_id = atbc.article_id) WHERE a.article_id = '" . (int)$blog_article_id . "'");

            // Get Category ID.
            if ($query_article->row['blog_category_id']) {
                $blog_category_id = $query_article->row['blog_category_id'];
            } else {
                $blog_category_id = 0;
            }

            // Get Category Path.
            $query_category_path = $this->db->query("SELECT TRIM('0_' FROM CONCAT(GROUP_CONCAT(bc2.parent_id ORDER BY bcp.level SEPARATOR '_'), '_', bc1.blog_category_id)) AS path FROM " . DB_PREFIX . "blog_category_path bcp LEFT JOIN " . DB_PREFIX . "blog_category bc1 ON (bcp.blog_category_id = bc1.blog_category_id) LEFT JOIN " . DB_PREFIX . "blog_category bc2 ON (bcp.path_id = bc2.blog_category_id) WHERE bcp.blog_category_id = '" . (int)$blog_category_id . "'");

            if (isset($query_category_path->row['path'])) {
                $path = $query_category_path->row['path'];
            }

            $blog_article_data = array(
                'path' => $path
            );
        }

        return $blog_article_data;
    }

    /**
     * Get Prepared Categories data.
     * 
     * @param array $IDs
     * 
     * @return array $prepared_data
     */
    public function getCategoriesPrepared($IDs) {
        $prepared_data = array();

        // Get Categories from DB.
        $query = $this->db->query("SELECT c1.category_id, c2.parent_id FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) WHERE c1.category_id IN (" . implode(',', $IDs) . ") ORDER BY c1.category_id, cp.level");

        $count_categories = count($query->rows);
        $last_id = (int)$query->rows[0]['category_id'];
        $prepared_data['category_' . $last_id]['path'] = '';

        // Get Categories data.
        for ($i = 0; $i < $count_categories; $i++) {
            if ($query->rows[$i]['category_id'] != $last_id) {
                $prepared_data['category_' . $last_id]['path'] .= $last_id;

                $last_id = (int)$query->rows[$i]['category_id'];
                $prepared_data['category_' . $last_id]['path'] = '';
            }

            if ($query->rows[$i]['parent_id']) {
                $prepared_data['category_' . $last_id]['path'] .= $query->rows[$i]['parent_id'] . '_';
            }

            if ($i == ($count_categories - 1)) {
                $prepared_data['category_' . $last_id]['path'] .= $query->rows[$i]['category_id'];
            }
        }

        return $prepared_data;
    }

    /**
     * Get Prepared Products data.
     * 
     * @param array $IDs
     * 
     * @return array $prepared_data
     */
    public function getProductsPrepared($IDs) {
        $prepared_data = array();
        $categories_data = array();

        // Get Products from DB.
        $query_products = $this->db->query("SELECT p.product_id, ptc.category_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_category ptc ON (p.product_id = ptc.product_id) WHERE p.product_id IN (" . implode(',', $IDs) . ") GROUP BY p.product_id ORDER BY p.product_id");

        $categories = array();

        // Get categories of products.
        foreach ($query_products->rows as $product) {
            if (!in_array($product['category_id'], $categories) && $product['category_id']) {
                $categories[] = $product['category_id'];
            }
        }

        if (!empty($categories)) {
            // Get Categories data from DB.
            $query_categories = $this->db->query("SELECT cp.category_id, c2.parent_id FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) WHERE cp.category_id IN (" . implode(',', $categories) . ") ORDER BY cp.category_id, cp.level");

            $count_categories = count($query_categories->rows);
            $last_id = (int)$query_categories->rows[0]['category_id'];
            $categories_data['category_' . $last_id]['path'] = '';

            // Get Categories data.
            for ($i = 0; $i < $count_categories; $i++) {
                if ($query_categories->rows[$i]['category_id'] != $last_id) {
                    $categories_data['category_' . $last_id]['path'] .= $last_id;

                    $last_id = (int)$query_categories->rows[$i]['category_id'];
                    $categories_data['category_' . $last_id]['path'] = '';
                }

                if ($query_categories->rows[$i]['parent_id']) {
                    $categories_data['category_' . $last_id]['path'] .= $query_categories->rows[$i]['parent_id'] . '_';
                }

                if ($i == ($count_categories - 1)) {
                    $categories_data['category_' . $last_id]['path'] .= $query_categories->rows[$i]['category_id'];
                }
            }

            // Set Products data.
            foreach ($query_products->rows as $product) {
                if (isset($categories_data['category_' . $product['category_id']]['path'])) {
                    $prepared_data['product_' . $product['product_id']]['path'] = $categories_data['category_' . $product['category_id']]['path'];
                }
            }
        }

        return $prepared_data;
    }

    /**
     * Get Prepared ocStore Blog Categories data.
     * 
     * @param array $IDs
     * 
     * @return array $prepared_data
     */
    public function getBlogCategoriesPrepared($IDs) {
        $prepared_data = array();

        $table_name = DB_PREFIX . 'blog_category';

        if ($this->tableExists($table_name)) {
            // Get Blog Categories from DB.
            $query = $this->db->query("SELECT bc1.blog_category_id, bc2.parent_id FROM " . DB_PREFIX . "blog_category_path bcp LEFT JOIN " . DB_PREFIX . "blog_category bc1 ON (bcp.blog_category_id = bc1.blog_category_id) LEFT JOIN " . DB_PREFIX . "blog_category bc2 ON (bcp.path_id = bc2.blog_category_id) WHERE bc1.blog_category_id IN (" . implode(',', $IDs) . ") ORDER BY bc1.blog_category_id, bcp.level");

            $count_categories = count($query->rows);
            $last_id = (int)$query->rows[0]['blog_category_id'];
            $prepared_data['blog_category_' . $last_id]['path'] = '';

            // Get Blog Categories data.
            for ($i = 0; $i < $count_categories; $i++) {
                if ($query->rows[$i]['blog_category_id'] != $last_id) {
                    $prepared_data['blog_category_' . $last_id]['path'] .= $last_id;

                    $last_id = (int)$query->rows[$i]['blog_category_id'];
                    $prepared_data['blog_category_' . $last_id]['path'] = '';
                }

                if ($query->rows[$i]['parent_id']) {
                    $prepared_data['blog_category_' . $last_id]['path'] .= $query->rows[$i]['parent_id'] . '_';
                }

                if ($i == ($count_categories - 1)) {
                    $prepared_data['blog_category_' . $last_id]['path'] .= $query->rows[$i]['blog_category_id'];
                }
            }
        }

        return $prepared_data;
    }

    /**
     * Get Prepared ocStore Blog Articles data.
     * 
     * @param array $IDs
     * 
     * @return array $prepared_data
     */
    public function getBlogArticlesPrepared($IDs) {
        $prepared_data = array();

        $table_name = DB_PREFIX . 'article';

        if ($this->tableExists($table_name)) {
            $categories_data = array();

            // Get Blog Articles from DB.
            $query_articles = $this->db->query("SELECT a.article_id, atbc.blog_category_id FROM " . DB_PREFIX . "article a LEFT JOIN " . DB_PREFIX . "article_to_blog_category atbc ON (a.article_id = atbc.article_id) WHERE a.article_id IN (" . implode(',', $IDs) . ") GROUP BY a.article_id ORDER BY a.article_id");

            $categories = array();

            // Get categories of articles.
            foreach ($query_articles->rows as $article) {
                if (!in_array($article['blog_category_id'], $categories) && $article['blog_category_id']) {
                    $categories[] = $article['blog_category_id'];
                }
            }

            if (!empty($categories)) {
                // Get Blog Categories data from DB.
                $query_categories = $this->db->query("SELECT bcp.blog_category_id, bc2.parent_id FROM " . DB_PREFIX . "blog_category_path bcp LEFT JOIN " . DB_PREFIX . "blog_category bc1 ON (bcp.blog_category_id = bc1.blog_category_id) LEFT JOIN " . DB_PREFIX . "blog_category bc2 ON (bcp.path_id = bc2.blog_category_id) WHERE bcp.blog_category_id IN (" . implode(',', $categories) . ") ORDER BY bcp.blog_category_id, bcp.level");

                $count_categories = count($query_categories->rows);
                $last_id = (int)$query_categories->rows[0]['blog_category_id'];
                $categories_data['blog_category_' . $last_id]['path'] = '';

                // Get Blog Categories data.
                for ($i = 0; $i < $count_categories; $i++) {
                    if ($query_categories->rows[$i]['blog_category_id'] != $last_id) {
                        $categories_data['blog_category_' . $last_id]['path'] .= $last_id;

                        $last_id = (int)$query_categories->rows[$i]['blog_category_id'];
                        $categories_data['blog_category_' . $last_id]['path'] = '';
                    }

                    if ($query_categories->rows[$i]['parent_id']) {
                        $categories_data['blog_category_' . $last_id]['path'] .= $query_categories->rows[$i]['parent_id'] . '_';
                    }

                    if ($i == ($count_categories - 1)) {
                        $categories_data['blog_category_' . $last_id]['path'] .= $query_categories->rows[$i]['blog_category_id'];
                    }
                }

                // Set Blog Articles data.
                foreach ($query_articles->rows as $article) {
                    if (isset($categories_data['blog_category_' . $article['blog_category_id']]['path'])) {
                        $prepared_data['blog_article_' . $article['article_id']]['path'] = $categories_data['blog_category_' . $article['blog_category_id']]['path'];
                    }
                }
            }
        }

        return $prepared_data;
    }

    /**
     * Remove HTML tags and their contents based on ID.
     * 
     * @param string $html
     * @param array $IDs
     * 
     * @return string $html
     */
    public function removeTagsByID($html, $IDs) {
        $doc = new DOMDocument();
        //$doc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $doc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR);
        $xpath = new DOMXPath($doc);

        // Find elements with specified ID values.
        foreach ($IDs as $id) {
            $tags = $xpath->query("//*[@id='$id']");

            // Remove elements.
            foreach ($tags as $tag) {
                $tag->parentNode->removeChild($tag);
            }
        }

        return $doc->saveHTML();
    }

    /**
     * A list of chosen tables.
     * 
     * @param string $tables_name
     * 
     * @return bool $exists
     */
    private function tableExists($tables_name) {
        return $this->db->query("SHOW TABLES LIKE '" . $tables_name . "'")->num_rows > 0;
    }
}