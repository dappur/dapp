
// Bind Blog to the Container
$container['blog'] = function ($container){
return new \Dappur\Dappurware\Blog($container);
};

// Add Twig Global to check if blog admin file exists.
// Used to show menu item.
if (file_exists( __DIR__ . '/../views/default/Admin/blog.twig')) {
    $container['view']->getEnvironment()->addGlobal('hasBlog', true);
}else{
    $container['view']->getEnvironment()->addGlobal('hasBlog', false);
}