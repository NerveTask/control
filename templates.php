<script type="text/tmpl" id="dashboard-view">
    <div class="post-list">

         {[ if(posts.length) { ]}

			<table class="table table-striped table-condensed table-bordered table-hover">

				<thead>
					<tr>
						<th class="column-title">Title</th>
						<th class="column-status">Status</th>
						<th class="column-priority">Priority</th>
						<th class="column-assigned">Assigned</th>
						<th class="column-due-date">Due Date</th>
					</tr>
				</thead>

				<tbody>

			{[ _.each(posts, function(post){ ]}

				<tr class="post nervetask">
                    <td class="column-title title"><a href="{{ post.get('link') }}">{{ post.get("title") }}</a></td>
					<td class="column-status">{{ post.get('link') }}</td>
					<td class="column-priority"></td>
					<td class="column-assigned"></td>
					<td class="column-due-date"></td>
				</tr>

            {[ }); ]}

				</tbody>

			</table>

        {[ } else { ]}

            <h2>No posts found</h2>

        {[ } ]}

        {[ if ( pages > 1 ) { ]}
            <div class="pagination-centered"><ul class="pagination">
            {[ for (var i = 1; i <= pages; i++) { ]}

                {[ className = (i == currentPage) ? ' class="active"' : '' ]}

                <li{{ className }}><a href="#/p/{{ i }}">{{ i }}</a></li>

            {[ } ]}

            </div></ul>

        {[ } ]}
    </div>
</script>

<script type="text/tmpl" id="single-task-view">
    <div class="post-list">
        <article <?php post_class( 'col-sm-9'); ?>>

			<header>
				<a href="<?php the_permalink(); ?>" class="post-id"><?php the_ID(); ?></a>
				<h1 class="entry-title">
					{{ post.get("title") }}
				</h1>
			</header>

            <div class="entry-content">{{ post.get('content') }}</div>

            <div id="comments">Loading...</div>
        </article>

		<aside class="nervetask-sidebar col-sm-3">
			Written by {{ post.get('author').get('name') }}
			on {{ post.get('date') }}
		</aside>
    </div>

</script>

<script type="text/tmpl" id="single-page-view">
    <div class="post-list">
        <article class="post">
            <h2>{{ post.get("title") }}</h2>

            <div class="entry-content">{{ post.get('content') }}</div>
        </article>
    </div>
</script>

<script type="text/tmpl" id="single-post-view">
    <div class="post-list">
        <article class="post">
            <h2>{{ post.get("title") }}</h2>

            <div class="entry-meta">
                Written by {{ post.get('author').get('name') }}
                on {{ post.get('date') }}
            </div>

            <div class="entry-content">{{ post.get('content') }}</div>

            <div id="comments">Loading...</div>
        </article>
    </div>
</script>

<script type="text/tmpl" id="comments-view">
    {[ if (comments.length) { ]}
        <h2 id="comments-title">{{ comments.length }} comments!</h2>

            <ol class="commentlist">
            {[ _.each(comments, function(comment){ ]}

                <li id="li-comment-{{ comment.get('ID') }}">
                    <article class="comment">
                        <footer>
                            <div class="comment-author vcard">
                                <div class="comment-avatar"><img class="avatar" src="{{ comment.get('author').avatar }}" alt="avatar" /></div>

                                <cite class="fn">{{ comment.get('author').name }} </cite> <span class="says">says:</span>
                            </div>
                        </footer>

                        {[ if (comment.get('status') !== 'approved' ) { ]}
                            <em><?php _e( 'Your comment is awaiting moderation.', 'wedevs' ); ?></em>
                            <br />
                        {[ } ]}

                        <div class="comment-content">{{ comment.get('content') }}</div>
                    </article>
                </li>

            {[ }); ]}
            </ol>

    {[ } else { ]}
        <h2 id="comments-title">No comments found!</h2>
    {[ } ]}


    {[ if (post.get('comment_status') === 'open') { ]}

        <div id="respond" class="comment-respond">
            <form action="<?php //echo site_url( '/wp-comments-post.php' ); ?>" method="post" id="commentform" class="comment-form">
                <?php if ( is_user_logged_in() ) : ?>
                    You are logged in
                <?php else : ?>
                    <?php
                    $commenter = wp_get_current_commenter();
                    $user = wp_get_current_user();
                    $user_identity = $user->exists() ? $user->display_name : '';

                    $req      = get_option( 'require_name_email' );
                    $aria_req = ( $req ? " required='required'" : '' );
                    $html5    = true;
                    $fields   =  array(
                        'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
                                    '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
                        'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
                                    '<input id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
                        'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website' ) . '</label> ' .
                                    '<input id="url" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
                    );
                    foreach ( $fields as $name => $field ) {
                        echo apply_filters( "comment_form_field_{$name}", $field ) . "\n";
                    }
                    ?>
                <?php endif; ?>

                <p class="comment-form-comment">
                    <label for="comment"><?php echo _x( 'Comment', 'noun' ); ?></label>
                    <textarea id="comment" name="comment" cols="45" rows="8"<?php echo $aria_req; ?>></textarea>
                </p>

                <p class="form-submit">
                    <input class="btn" name="submit" type="submit" id="submit" value="<?php echo esc_attr( __( 'Post Comment' ) ); ?>" />
                    <input type='hidden' name='comment_post_ID' value='{{ post.get('ID') }}' id='comment_post_ID' />
                    <input type='hidden' name='comment_parent' id='comment_parent' value='0' />
                </p>
            </form>
        </div>
    {[ } ]}

</script>