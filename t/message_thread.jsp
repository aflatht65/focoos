<%@ page contentType="text/html; charset=UTF-8" %>

<%@ page import="focoos.dao.*,focoos.entities.*,java.text.DateFormat,java.text.SimpleDateFormat,java.util.List,java.util.ArrayList,java.lang.NumberFormatException" %>

<%
	if(session.getAttribute("userData") == null) {
		response.sendRedirect("login.jsp?redirectTo="+request.getRequestURL().toString());
	}
	DateFormat dft = new SimpleDateFormat("dd-MM-yyyy hh:ss");
	Student inuser = (Student)session.getAttribute("userData");
	List<PrivateMessage> pmsgs = new ArrayList<PrivateMessage>();
	StudentsDao studentsDao = new StudentsDao();
	studentsDao.open();
	Student sender = new Student();
	sender.id = -1;
	try {
		sender = studentsDao.getStudentById(Integer.parseInt(request.getParameter("sender")));
	} catch(NumberFormatException e) {
		response.sendRedirect("message_inbox.jsp");
	}
	studentsDao.close();
	if(inuser != null) {
		PrivateMessagesDao pmsgsDao = new PrivateMessagesDao();
		pmsgsDao.open();
		pmsgs = pmsgsDao.getPrivateMessagesBySenderAndReceiver(sender, inuser);
		pmsgsDao.close();
	}
%>

<%@ include file="includes/header.inc.jsp" %>

<div class="row full-width">
	<%@ include file="includes/sidemenu.inc.jsp" %>
	
	<h3>Συζητήσεις με <a href="profile_view.jsp?id=<%= sender.id %>"><%= sender.name %> <%= sender.surname %></a></h3>
	
	<div class="large-10 columns main posts conversations">
		<% for(PrivateMessage pmsg : pmsgs) { %>
		<div class="row collapse post">
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
		<div class="row">
			<div class="large-12 columns">
				<div class="panel has-form">
					<form action="servlet/NewPrivateMessageServlet?to=<%= sender.id %>" method="POST">
						<div class="row collapse">
							<div class="small-11 columns">
								<input type="text" name="message" class="no-margin-bottom" placeholder="Γράψε ένα μήνυμα..." />
							</div>
							<div class="small-1 columns">
								<input type="submit" name="submitted" class="no-margin-bottom button postfix" value="&#x2192;" />
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<%@ include file="includes/footer.inc.jsp" %>