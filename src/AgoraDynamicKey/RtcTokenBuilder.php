<?php

namespace AbdullahFaqeir\LaravelAgoraApi\AgoraDynamicKey;

class RtcTokenBuilder
{
    public const RoleAttendee = 0;

    public const RolePublisher = 1;

    public const RoleSubscriber = 2;

    public const RoleAdmin = 101;

    # appID: The App ID issued to you by Agora. Apply for a new App ID from
    #        Agora Dashboard if it is missing from your kit. See Get an App ID.
    # appCertificate:	Certificate of the application that you registered in
    #                  the Agora Dashboard. See Get an App Certificate.
    # channelName:Unique channel name for the AgoraRTC session in the string format
    # uid: User ID. A 32-bit unsigned integer with a value ranging from
    #      1 to (232-1). optionalUid must be unique.
    # role: Role_Publisher = 1: A broadcaster (host) in a live-broadcast profile.
    #       Role_Subscriber = 2: (Default) A audience in a live-broadcast profile.
    # privilegeExpireTs: represented by the number of seconds elapsed since
    #                    1/1/1970. If, for example, you want to access the
    #                    Agora Service within 10 minutes after the token is
    #                    generated, set expireTimestamp as the current
    #                    timestamp + 600 (seconds)./
    public static function buildTokenWithUid(?string $appID, ?string $appCertificate, ?string $channelName, ?string $uid, int $role, int $privilegeExpireTs): string
    {
        return self::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpireTs);
    }

    # appID: The App ID issued to you by Agora. Apply for a new App ID from
    #        Agora Dashboard if it is missing from your kit. See Get an App ID.
    # appCertificate:	Certificate of the application that you registered in
    #                  the Agora Dashboard. See Get an App Certificate.
    # channelName:Unique channel name for the AgoraRTC session in the string format
    # userAccount: The user account.
    # role: Role_Publisher = 1: A broadcaster (host) in a live-broadcast profile.
    #       Role_Subscriber = 2: (Default) A audience in a live-broadcast profile.
    # privilegeExpireTs: represented by the number of seconds elapsed since
    #                    1/1/1970. If, for example, you want to access the
    #                    Agora Service within 10 minutes after the token is
    #                    generated, set expireTimestamp as the current
    public static function buildTokenWithUserAccount(?string $appID, ?string $appCertificate, ?string $channelName, ?string $userAccount, int $role, int $privilegeExpireTs): string
    {
        $token = AccessToken::init($appID, $appCertificate, $channelName, $userAccount);
        $Privileges = AccessToken::Privileges;
        $token?->addPrivilege($Privileges["kJoinChannel"], $privilegeExpireTs);
        if (in_array($role, [self::RoleAttendee, self::RolePublisher, self::RoleAdmin], true)) {
            $token?->addPrivilege($Privileges["kPublishVideoStream"], $privilegeExpireTs);
            $token?->addPrivilege($Privileges["kPublishAudioStream"], $privilegeExpireTs);
            $token?->addPrivilege($Privileges["kPublishDataStream"], $privilegeExpireTs);
        }

        return $token?->build();
    }
}
