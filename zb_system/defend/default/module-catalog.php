
{if $style==2}

{elseif $style==1}

{else}

{/if}


        $i = $zbp->modulesbyfilename['catalog']->MaxLi;
        $j = 0;
        if ($zbp->option['ZC_MODULE_CATALOG_STYLE'] == '2') {
            foreach ($zbp->categorysbyorder as $key => $value) {
                if ($value->Level == 0) {
                    $s .= '<li class="li-cate"><a href="' . $value->Url . '">' . $value->Name . '</a><!--' . $value->ID . 'begin--><!--' . $value->ID . 'end--></li>';
                }
                $j += 1;
                if ($i != 0 && $j >= $i) {
                    break;
                }
            }

            for ($i = 1; $i <= 3; $i++) {
                // 此处逻辑仍要继续修改
                foreach ($zbp->categorysbyorder as $key => $value) {
                    if ($value->Level == $i) {
                        $s = str_replace('<!--' . $value->ParentID . 'end-->', '<li class="li-subcate"><a href="' . $value->Url . '">' . $value->Name . '</a><!--' . $value->ID . 'begin--><!--' . $value->ID . 'end--></li><!--' . $value->ParentID . 'end-->', $s);
                    }
                }
            }

            foreach ($zbp->categorysbyorder as $key => $value) {
                $s = str_replace('<!--' . $value->ID . 'begin--><!--' . $value->ID . 'end-->', '', $s);
            }
            foreach ($zbp->categorysbyorder as $key => $value) {
                $s = str_replace('<!--' . $value->ID . 'begin-->', '<ul class="ul-subcates">', $s);
                $s = str_replace('<!--' . $value->ID . 'end-->', '</ul>', $s);
            }

        } elseif ($zbp->option['ZC_MODULE_CATALOG_STYLE'] == '1') {
            foreach ($zbp->categorysbyorder as $key => $value) {
                $s .= '<li>' . $value->Symbol . '<a href="' . $value->Url . '">' . $value->Name . '</a></li>';
                $j += 1;
                if ($i != 0 && $j >= $i) {
                    break;
                }

            }
        } else {
            foreach ($zbp->categorysbyorder as $key => $value) {
                $s .= '<li><a href="' . $value->Url . '">' . $value->Name . '</a></li>';
                $j += 1;
                if ($i != 0 && $j >= $i) {
                    break;
                }

            }
        }

        return $s;



{foreach $catelogs as $catalog}
<li><a href="{$catalog.Url}">{$catalog.Title}</a></li>
{/foreach}