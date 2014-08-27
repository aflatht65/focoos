<%@ page contentType="text/html; charset=UTF-8" %>

<%@ page import="focoos.dao.*,focoos.entities.*,java.text.DateFormat,java.text.SimpleDateFormat,java.util.List,java.util.ArrayList" %>

<%
	if(session.getAttribute("userData") == null) {
		response.sendRedirect("login.jsp?redirectTo="+request.getRequestURL().toString());
	}
	DateFormat dft = new SimpleDateFormat("dd-MM-yyyy hh:ss");
	Student inuser = (Student)session.getAttribute("userData");
	List<PrivateMessage> pmsgs = new ArrayList<PrivateMessage>();
	if(inuser != null) {
		PrivateMessagesDao pmsgsDao = new PrivateMessagesDao();
		pmsgsDao.open();
		pmsgs = pmsgsDao.getLastPrivateMessagesByReceiver(inuser);
		pmsgsDao.close();
	}
%>

<%@ include file="includes/header.inc.jsp" %>

<div class="row full-width">
	<%@ include file="includes/sidemenu.inc.jsp" %>
	
	<h3>Συζητήσεις</h3>
	
	<div class="large-10 columns main posts conversations">
		<% if(pmsgs.size() > 0) { %>
			<% for(PrivateMessage pmsg : pmsgs) { %>
			<div class="row collapse post link" onclick="window.location = 'message_thread.jsp?sender=<%= pmsg.sender.id %>';">
				<div class="large-1 columns photo">
					<img class="avatar medium" src="<%= pmsg.sender.avatarUrl != null ? pmsg.sender.avatarUrl : "http://placehold.it/60x60.png" %>" />
				</div>
				<div class="large-11 columns">
					<div class="row header">
						<div class="large-12 columns">
							<a href="profile_view.jsp?id=<%= pmsg.sender.id %>"><%= pmsg.sender.name %> <%= pmsg.sender.surname %></a> <small class="time"><%= dft.format(pmsg.creationDate) %></small>
						</div>
					</div>
					<div class="row content">
						<div class="large-12 columns">
							<%= pmsg.message %>
						</div>
					</div>
				</div>
			</div>
			<% } %>
		<% } else { %>
			<h5>Δεν έχεις ακόμα συνομιλίες</h5>
		<% } %>
	</div>
</div>

<%@ include file="includes/footer.inc.jsp" %>
