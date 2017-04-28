{% if hasBlog %}
			<li>
			    <a href="{{ path_for('admin-blog')}}">
			        <i class="fa fa-fw fa-clipboard"></i> Blog Posts
			    </a>
			</li>
			{% endif %}

            {% if auth.hasAccess('user.create') %}