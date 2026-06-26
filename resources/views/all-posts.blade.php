<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Listing</title>
</head>
<body>

        <h2> All Sites Posts List</h2>
<?//php echo "<pre>"; print_r($allPosts); die; ?>
        @foreach($allPosts as $site) 
        <?//php $key = $site['site_key']; ?> 

            <h3>
               <!-- <a href="{{ $site['site_url'] }}" target="_blank">{{ $site['site_name'] }}</a> -->
            </h3>

            <ul>
                
             
                    <li>
                        <a href="{{ $site['site_link'] }}" target="_blank">
                            {{ $site['site_link'] }}
                        </a>

                           
                        <?php if($site['site_key'] == '1'){ ?>                    
                                <button><a href="{{ $site['site_link'] }}" target="_blank"> {{ $site['site_name'] }}</a></button>
                        <?php } ?>

                        <?php if($site['site_key'] == '2'){ ?>                    
                                <button><a href="{{ $site['site_link'] }}" target="_blank"> {{ $site['site_name'] }}</a></button>
                        <?php } ?>
                            
                        
                        
                        

                        <!-- //     <button></button>
                        //     }if(isstories){                            
                        //     <button></button>
                        //     }if(isstories){                            
                        //     <button></button>
                        //     }if(isstories){                            
                        //     <button></button>
                        //     }
                        //     ?> -->
                        <!-- // <button></button>
                        // <button></button>
                        // <button></button>
                        // <button></button> -->

                    </li>
                

            </ul>


       @endforeach     

        <a href=" /dashboard ">Back to Dashboard</a>
    
</body>
</html>